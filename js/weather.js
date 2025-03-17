document.addEventListener("DOMContentLoaded", function () {
  getUserLocation();
});

async function getUserLocation() {
  if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        await getWeather(lat, lon);
      },
      (error) => {
        console.error("Lỗi lấy vị trí:", error);
        document.getElementById(
          "weatherInfo"
        ).innerHTML = `<p class="text-danger">Không thể lấy vị trí. Vui lòng bật GPS.</p>`;
      }
    );
  } else {
    document.getElementById(
      "weatherInfo"
    ).innerHTML = `<p class="text-danger">Trình duyệt không hỗ trợ định vị.</p>`;
  }
}

async function getWeather(lat, lon) {
  const API_KEY = "789a73a7c229e5767f485c5ab106ef0c";
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric&lang=vi`;

  try {
    const response = await fetch(apiUrl);
    const data = await response.json();

    if (response.ok) {
      const temp = data.main.temp;
      const feelsLike = data.main.feels_like;
      const humidity = data.main.humidity;
      const windSpeed = data.wind.speed;
      const pressure = data.main.pressure;
      const visibility = data.visibility / 1000;
      const weatherDesc = data.weather[0].description;
      const weatherIcon = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;

      // Cập nhật giao diện
      document.getElementById("weatherInfo").innerHTML = `
              <img src="${weatherIcon}" alt="Weather icon">
              <h5>${data.name}, ${data.sys.country}</h5>
              <p><strong>Nhiệt độ:</strong> ${temp}°C</p>
              <p><strong>Cảm giác như:</strong> ${feelsLike}°C</p>
              <p><strong>Độ ẩm:</strong> ${humidity}%</p>
              <p><strong>Tốc độ gió:</strong> ${windSpeed} m/s</p>
              <p><strong>Áp suất:</strong> ${pressure} hPa</p>
              <p><strong>Tầm nhìn:</strong> ${visibility} km</p>
              <p><strong>Thời tiết:</strong> ${
                weatherDesc.charAt(0).toUpperCase() + weatherDesc.slice(1)
              }</p>
          `;
    } else {
      document.getElementById(
        "weatherInfo"
      ).innerHTML = `<p class="text-danger">Không thể lấy dữ liệu thời tiết.</p>`;
    }
  } catch (error) {
    document.getElementById(
      "weatherInfo"
    ).innerHTML = `<p class="text-danger">Lỗi khi tải dữ liệu thời tiết.</p>`;
  }
}
