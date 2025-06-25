// modal.js
document.addEventListener("DOMContentLoaded", () => {
  const aboutBtn = document.querySelector(".about-btn");
  const contactBtn = document.querySelector(".contact-btn");
  const aboutModal = document.getElementById("aboutModal");
  const contactModal = document.getElementById("contactModal");
  const closeButtons = document.querySelectorAll(".close");

  aboutBtn?.addEventListener("click", () => {
    aboutModal.showModal();
  });

  contactBtn?.addEventListener("click", () => {
    contactModal.showModal();
  });

  closeButtons.forEach((btn) =>
    btn.addEventListener("click", () => {
      btn.closest("dialog").close();
    })
  );
});
