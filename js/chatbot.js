document
  .getElementById("chatForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();

    const userInput = document.getElementById("userMessage").value.trim();
    if (!userInput) return;

    // Hiển thị tin nhắn của người dùng
    const chatBox = document.getElementById("chatBox");
    chatBox.innerHTML += `<div class="text-end"><strong>Bạn:</strong> ${userInput}</div>`;

    // Xóa nội dung input sau khi gửi
    document.getElementById("userMessage").value = "";

    try {
      const aiResponse = await getGeminiResponse(userInput);
      chatBox.innerHTML += `<div><strong>Chatbot:</strong> ${aiResponse}</div>`;
    } catch (error) {
      chatBox.innerHTML += `<div class="text-danger"><strong>Chatbot:</strong> Lỗi khi lấy phản hồi.</div>`;
    }

    // Cuộn xuống cuối chatbox để xem tin nhắn mới nhất
    chatBox.scrollTop = chatBox.scrollHeight;
  });

async function getGeminiResponse(prompt) {
  const API_KEY = "AIzaSyBoEbJjJvMsyQi02ycX4HYrNv_63gN7rfw"; // 🔴 Thay bằng API Key thực tế
  const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${API_KEY}`;

  const response = await fetch(apiUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      contents: [
        {
          parts: [{ text: prompt }],
        },
      ],
    }),
  });

  const data = await response.json();
  return (
    data.candidates?.[0]?.content?.parts?.[0]?.text ||
    "Xin lỗi, tôi không hiểu câu hỏi của bạn."
  );
}
