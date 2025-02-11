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
