# Despliegue en EC2

Flujo: push a `main` > GitHub Actions (PHP lint) > rol IAM vía OIDC > AWS SSM Run Command > `mitos-deploy` en la EC2
descarga el commit, sincroniza el tema y recarga PHP-FPM. No usa SSH ni llaves guardadas en GitHub.

## 1. Aprovisionar el servidor (una sola vez)

Hay que ejecutarlo con acceso al servidor (SSH o `aws ssm start-session`):

```bash
scp -i ~/.ssh/mitosdelsabor.pem -r deploy ubuntu@13.218.214.215:~/
ssh -i ~/.ssh/mitosdelsabor.pem ubuntu@13.218.214.215
cd ~/deploy
sudo SITE_URL=https://mitosdelsabor.pe \
     DB_HOST=mitosverdadesdb.cohwqsywee0c.us-east-1.rds.amazonaws.com \
     DB_NAME=mydbmitosverdades DB_USER=admin ./provision.sh
```

La contraseña de la base se pide por prompt y solo queda en `/var/www/mitos/wp-config.php` (640).

El HTTPS de https://mitosdelsabor.pe termina en el Application Load Balancer (certificado ACM), así que el servidor solo recibe HTTP en el puerto 80.
Conviene limitar ese puerto 80 al Security Group del balanceador, no a `0.0.0.0/0`.

Después: importar la base de datos y `wp-content/uploads`.

## 2. Configurar AWS y GitHub

Seguir [aws-setup.md](aws-setup.md): proveedor OIDC, rol, secrets, y al final cerrar el puerto 22.
