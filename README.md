# MySite

---

# Инструкция по развертыванию на Ubuntu

# 1. Установка и настройка Nginx
```
listen       80;
server_name  localhost;
root   /var/www/html;

access_log  /var/log/nginx/host.access.log  main;

location / {
    proxy_pass http://localhost:8080;
}
```

# 2. Установка Docker compose

```bash
sudo curl -fsSL get.docker.com -o get-docker.sh && sudo sh get-docker.sh
```

# 3. Установка и настройка Git
```bash
sudo apt install git
sudo git clone
```