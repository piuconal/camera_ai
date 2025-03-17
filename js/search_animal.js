async function searchAnimal() {
  const input = document
    .getElementById("searchAnimal")
    .value.toLowerCase()
    .trim();
  const animalList = document.getElementById("animalList");
  const animalInfo = document.getElementById("animalInfo");

  // Xóa nội dung cũ trước khi hiển thị kết quả mới
  animalList.innerHTML = "";
  animalInfo.innerHTML = "";

  if (!input) return;

  try {
    const response = await fetch("animals.json");
    const animals = await response.json();

    // Lọc kết quả đúng và loại bỏ trùng lặp
    const filteredAnimals = animals.filter((animal) =>
      animal.name.toLowerCase().includes(input)
    );

    // Nếu tìm thấy động vật
    if (filteredAnimals.length > 0) {
      animalList.innerHTML = ""; // Đảm bảo không bị lặp

      filteredAnimals.forEach((animal) => {
        const listItem = document.createElement("li");
        listItem.textContent = animal.name;
        listItem.classList.add("list-group-item", "cursor-pointer");
        listItem.onclick = () => showAnimalInfo(animal);

        // Kiểm tra trùng lặp trước khi thêm vào danh sách
        if (
          ![...animalList.children].some(
            (child) => child.textContent === animal.name
          )
        ) {
          animalList.appendChild(listItem);
        }
      });
    } else {
      animalList.innerHTML = `<li class="text-danger">Không tìm thấy kết quả.</li>`;
    }
  } catch (error) {
    animalList.innerHTML = `<li class="text-danger">Lỗi khi tải dữ liệu.</li>`;
  }
}

function showAnimalInfo(animal) {
  const animalInfo = document.getElementById("animalInfo");
  animalInfo.innerHTML = `
      <div class="modal fade" id="animalModal" tabindex="-1" aria-labelledby="animalModalLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="animalModalLabel">${animal.name} (${animal.scientific_name})</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <img src="${animal.image}" alt="${animal.name}" style="width: 100%; border-radius: 10px;">
                      <p><strong>Môi trường sống:</strong> ${animal.habitat}</p>
                      <p><strong>Chế độ ăn:</strong> ${animal.diet}</p>
                      <p><strong>Tuổi thọ:</strong> ${animal.lifespan}</p>
                      <p>${animal.description}</p>
                  </div>
              </div>
          </div>
      </div>
  `;

  // Kích hoạt modal
  const modal = new bootstrap.Modal(document.getElementById("animalModal"));
  modal.show();
}
