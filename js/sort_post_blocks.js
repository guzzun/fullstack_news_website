// sort post_block
document.addEventListener("DOMContentLoaded", () => {
  const post_blocks = document.querySelectorAll(".post_block");
  const noBlocksMessage = document.getElementById("no_blocks_message");

  window.sort_post_blocks_display_all = () => {
    post_blocks.forEach((post_block) => {
      post_block.style.display = "block";
    });
    noBlocksMessage.style.display = "none";
  };

  window.sort_post_blocks = (category) => {
    let found = false;
    post_blocks.forEach((post_block) => {
      let blockType = post_block.getAttribute("blockType");
      if (blockType === category) {
        found = true;
        post_block.style.display = "block";
      } else {
        post_block.style.display = "none";
      }
    });

    // Afiseaza mesajul de informare daca nu s-a gasit niciun bloc de categoria dorita
    if (!found) {
      noBlocksMessage.style.display = "block";
    } else {
      noBlocksMessage.style.display = "none";
    }

    // Sterge noBlocksMessage dupa 3 secunde
    setTimeout(() => {
      noBlocksMessage.style.display = "none";
    }, 3000);
  };
});
