import re, os, shutil, json
NM='node_modules'
HERE=os.path.dirname(os.path.abspath(__file__))
OUT=HERE                                   # fonts/-kansio
# Käyttö: npm i @fontsource-variable/{playfair-display,nunito-sans,eb-garamond,lora,jetbrains-mono,cinzel} @fontsource/{great-vibes,poppins,cinzel-decorative}
#        python3 fonts/build_fonts.py   (ajetaan kansiossa, jossa node_modules on)
os.makedirs(OUT, exist_ok=True); os.makedirs(OUT+'/licenses', exist_ok=True)
# (paketti, css-tiedostot, nimi sivuston CSS:ssä)
SPEC=[
 ('@fontsource-variable/playfair-display', ['wght.css','wght-italic.css'], 'Playfair Display'),
 ('@fontsource-variable/nunito-sans',      ['wght.css','wght-italic.css'], 'Nunito Sans'),
 ('@fontsource-variable/eb-garamond',      ['wght.css','wght-italic.css'], 'EB Garamond'),
 ('@fontsource-variable/lora',             ['wght.css','wght-italic.css'], 'Lora'),
 ('@fontsource-variable/jetbrains-mono',   ['wght.css'],                   'JetBrains Mono'),
 ('@fontsource/great-vibes',               ['400.css'],                    'Great Vibes'),
 ('@fontsource/poppins',                   ['300.css','400.css'],          'Poppins'),
 # Verkkokirjasto (/verkkokirjasto/)
 ('@fontsource-variable/cinzel',           ['wght.css'],                   'Cinzel'),
 ('@fontsource/cinzel-decorative',         ['700.css'],                    'Cinzel Decorative'),
]
blocks=[]; total=0; files=[]
for pkg, cssfiles, fam in SPEC:
    ver=json.load(open(f'{NM}/{pkg}/package.json'))['version']
    for cf in cssfiles:
        css=open(f'{NM}/{pkg}/{cf}').read()
        for m in re.finditer(r'/\* ([^*]+) \*/\s*@font-face\s*\{(.*?)\}', css, re.S):
            name, body = m.group(1).strip(), m.group(2)
            if not re.search(r'-latin(-ext)?-', name): continue          # vain latin + latin-ext
            src=re.search(r"url\(\./files/([^)]+\.woff2)\)\s*format\('([^']+)'\)", body)
            fn, fmt = src.group(1), src.group(2)
            shutil.copy(f'{NM}/{pkg}/files/{fn}', f'{OUT}/{fn}'); files.append(fn)
            total+=os.path.getsize(f'{OUT}/{fn}')
            style=re.search(r'font-style:\s*([^;]+);', body).group(1)
            weight=re.search(r'font-weight:\s*([^;]+);', body).group(1)
            ur=re.search(r'unicode-range:\s*([^;]+);', body).group(1)
            blocks.append(f"/* {name} · {pkg} {ver} */\n@font-face {{\n  font-family: '{fam}';\n  font-style: {style};\n  font-weight: {weight};\n  font-display: swap;\n  src: url('fonts/{fn}') format('{fmt}');\n  unicode-range: {ur};\n}}")
    lic=[f for f in os.listdir(f'{NM}/{pkg}') if f.upper().startswith('LICENSE')]
    for f in lic: shutil.copy(f'{NM}/{pkg}/{f}', f"{OUT}/licenses/{fam.replace(' ','-')}-{f}")
head = """/* =========================================================
   Fontit omalta palvelimelta (ei Google Fonts -pyyntöjä).
   Lähde: Fontsource-paketit, lisenssi SIL Open Font License 1.1 (fonts/licenses/).
   Vain latin + latin-ext (latvian ā ē ī ū č ģ ķ ļ ņ š ž); selain lataa
   tiedoston vasta, kun sivulla on sen merkkejä (unicode-range).
   Muuttuvapainoiset fontit: yksi tiedosto kattaa kaikki painot.
   Päivitys: build_fonts.py (Fontsource) → tämä tiedosto ja fonts/-kansio.
   ========================================================= */
"""
open(os.path.join(os.path.dirname(HERE),'fonts.css'),'w').write(head+'\n'+'\n\n'.join(blocks)+'\n')
print(len(blocks),'@font-face', len(files),'tiedostoa', round(total/1024),'kt')
