console.log("Node.js Worker başlatıldı. 5 dakikdada bir istek atılacak");
const targetUrl = 'http://todo_backend/send-reminders';
const intervalTime=300000;

async function sendRequest(){
    try{
        console.log(`[${new Date().toISOString()}] İstek atılıyor: ${targetUrl}`);
        const response=await fetch(targetUrl);
        const data=await response.json();
        console.log("Cevap başarılı:", data.id);
    }catch(error){
        console.error("İstek sırasında bir hata oluştu:", error.message);
    }
}
sendRequest();
setInterval(sendRequest, intervalTime);