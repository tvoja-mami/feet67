# Nastavitev strežnika
## Postavitev strežnika

### VirtualBox konfiguracija
- **Operacijski sistem**: Ubuntu Server 22.04 LTS
- **RAM**: 4 GB
- **HDD**: 25 GB
- **Omrežje**: Bridged Adapter
- **Prenos ISO**: `ubuntu-22.04.3-live-server-amd64.iso`

### Osnovna nastavitev Ubuntu Server
```bash
sudo apt update
sudo apt upgrade -y
sudo apt install -y curl wget vim git

Namestitev Apache2
bash

sudo apt install -y apache2
sudo systemctl enable apache2
sudo systemctl start apache2
sudo systemctl status apache2

Namestitev MySQL
bash

sudo apt install -y mysql-server
sudo mysql_secure_installation
sudo systemctl enable mysql
sudo systemctl start mysql

Namestitev PHP
bash

sudo apt install -y php libapache2-mod-php php-mysql
sudo apt install -y php-curl php-gd php-mbstring php-xml php-zip
sudo systemctl restart apache2

Nastavitev dostopa z ngrok
bash

sudo snap install ngrok
ngrok config add-authtoken YOUR_AUTH_TOKEN
ngrok http 80

Github konfiguracija
bash

git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
git clone https://github.com/your-repo/project.git

Preverjanje delovanja

    Apache: http://server-ip

    PHP: php -v

    MySQL: sudo mysql -u root -p

Strežnik je dostopen preko lokalnega omrežja in preko ngrok javnega naslova.
