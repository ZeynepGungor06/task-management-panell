# Hafif bir Node.js sürümü kullanıyoruz
FROM node:18-alpine

# Konteyner içindeki çalışma dizinimiz
WORKDIR /app

# app.js dosyasını konteynerin içine kopyala
COPY app.js .

# Konteyner ayağa kalktığında çalıştırılacak komut
CMD ["node", "app.js"]