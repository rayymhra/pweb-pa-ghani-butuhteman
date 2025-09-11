// Fitur Search Teman
function searchTeman() {
  let input = document.getElementById("searchInput").value.toLowerCase();
  let temanCards = document.querySelectorAll(".teman-card");

  temanCards.forEach(card => {
    let nama = card.getAttribute("data-nama").toLowerCase();
    if (nama.includes(input)) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}
