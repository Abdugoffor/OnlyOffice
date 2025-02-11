Docker orqali OnlyOffice yuklash
```
sudo docker run -itd --name onlyoffice-document-server \
  -p 8090:80 \
  onlyoffice/documentserver
```
OnlyOffice serverni ngrok orqali https ga aylantrish
```
ngrok http 8089
```
OnlyOffice token siz murojat qilish uchun:
```
docker exec -it COTAINER_ID bash
```
```
nano /etc/onlyoffice/documentserver local.json
```
"token": {
        "enable": {
          "request": {
            "inbox": false,
            "outbox": false
          },
          "browser": false
        },
