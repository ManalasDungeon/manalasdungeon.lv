#!/bin/sh
# Testien "sendmail": tallentaa viestin tiedostoon, ei lähetä mitään.
cat > "${MAIL_DIR:-/tmp}/$(date +%s%N).eml"
