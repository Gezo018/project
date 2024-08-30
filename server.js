const express = require('express');
const bodyParser = require('body-parser');
const { Client } = require('@line/bot-sdk');

const app = express();
const port = 3000;

const config = {
  channelAccessToken: 'YOUR_CHANNEL_ACCESS_TOKEN',
  channelSecret: 'YOUR_CHANNEL_SECRET',
};

const client = new Client(config);

app.use(bodyParser.json());

app.post('/webhook', (req, res) => {
  const orderData = req.body;

  // สร้างข้อความที่จะส่งไปยัง LINE OA
  const message = `
    มีการสั่งซื้อเค้กใหม่:
    ชื่อ: ${orderData.name}
    E-mail: ${orderData.email}
    เบอร์โทร: ${orderData.phone}
    รสชาติ: ${orderData.flavor}
    ขนาดปอนด์: ${orderData.pound}
    วันที่รับ: ${orderData.date}
    ข้อความบนเค้ก: ${orderData.message}
  `;

  // ส่งข้อความไปยัง LINE OA
  client.pushMessage('YOUR_USER_ID', {
    type: 'text',
    text: message,
  }).then(() => {
    res.sendStatus(200);
  }).catch((err) => {
    console.error(err);
    res.sendStatus(500);
  });
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
