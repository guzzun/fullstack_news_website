// sidemenu
document.addEventListener("DOMContentLoaded", () => {
  const sideMenu = document.getElementById("mySideMenu");
  const page = document.querySelector(".page");
  const openMenu = document.getElementById("openMenu");
  const openMenuMobile = document.getElementById("openMenuMobile");
  const closeMenu = document.getElementById("closeMenu");
  const openModalSidebar = document.getElementById("open_modal_sidebar");

  const openSideMenu = () => {
    sideMenu.style.width = "250px";
    page.classList.add("content_mask");
  };

  const closeSideMenu = () => {
    sideMenu.style.width = "0";
    page.classList.remove("content_mask");
  };

  openMenu.addEventListener("click", openSideMenu);
  if (openMenuMobile) {
    openMenuMobile.addEventListener("click", openSideMenu);
  }

  closeMenu.addEventListener("click", closeSideMenu);
  if (openModalSidebar) {
    openModalSidebar.addEventListener("click", closeSideMenu);
  }
});

// switch_mode
document.addEventListener("DOMContentLoaded", () => {
  const switchMode = document.getElementById("switchMode");
  const switchModeMobile = document.getElementById("switchModeMobile");

  const toggleDarkMode = () => {
    const isDarkMode = document.body.classList.toggle("dark_mode");
    if (isDarkMode) {
      localStorage.setItem("darkMode", "dark");
    } else {
      localStorage.removeItem("darkMode");
    }
  };

  if (switchMode) {
    switchMode.addEventListener("click", toggleDarkMode);
  }

  if (switchModeMobile) {
    switchModeMobile.addEventListener("click", toggleDarkMode);
  }

  // Verifică starea din localStorage la încărcarea paginii
  if (localStorage.getItem("darkMode")) {
    document.body.classList.add("dark_mode");
  } else {
    document.body.classList.remove("dark_mode");
  }
});

// const btnDarkMode = document.getElementById("switchMode");

// // 1. Check Dark-Mode value user system settings
// if (
//   window.matchMedia &&
//   window.matchMedia("(prefers-color-scheme: dark)").matches
// ) {
//   btnDarkMode.classList.add("dark-mode-btn--active");
//   document.body.classList.add("dark_mode");
// } else {
//   btnDarkMode.classList.remove("dark-mode-btn--active");
//   document.body.classList.remove("dark_mode");
// }

// // 2. Check Dark-Mode value localStorage
// if (localStorage.getItem("darkMode") === "dark") {
//   btnDarkMode.classList.add("dark-mode-btn--active");
//   document.body.classList.add("dark_mode");
// } else if (localStorage.getItem("darkMode") === "light") {
//   btnDarkMode.classList.remove("dark-mode-btn--active");
//   document.body.classList.remove("dark_mode");
// }

// // 3. Change Dark-Mode when change system settings without manual refresh
// window
//   .matchMedia("(prefers-color-scheme: dark)")
//   .addEventListener("change", (event) => {
//     const newColorScheme = event.matches ? "dark" : "light";

//     if (newColorScheme === "dark") {
//       btnDarkMode.classList.add("dark-mode-btn--active");
//       document.body.classList.add("dark");
//       localStorage.setItem("darkMode", "dark");
//     } else {
//       btnDarkMode.classList.remove("dark-mode-btn--active");
//       document.body.classList.remove("dark");
//       localStorage.setItem("darkMode", "light");
//     }
//   });

// // Switch Dark-Mode
// btnDarkMode.onclick = function () {
//   btnDarkMode.classList.toggle("dark-mode-btn--active");
//   const isDark = document.body.classList.toggle("dark");

//   if (isDark) {
//     localStorage.setItem("darkMode", "dark");
//   } else {
//     localStorage.setItem("darkMode", "light");
//   }
// };

// Scroll Up
let mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
  scrollFunction();
};

const scrollFunction = () => {
  if (
    document.body.scrollTop > 200 ||
    document.documentElement.scrollTop > 200
  ) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
};

// When the user clicks on the button, scroll to the top of the document
const topFunction = () => {
  // add smooth
  document.body.style.scrollBehavior = "smooth";
  document.documentElement.style.scrollBehavior = "smooth";

  // Scroll Up
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
};

//==============================================================================================
// Message transition
setTimeout(() => {
  const messages = document.querySelectorAll(".message");

  messages.forEach((message) => {
    message.style.opacity = "0";

    setTimeout(() => {
      message.style.display = "none";
    }, 500);
  });
}, 2000);

//Select all checkbox
document.addEventListener("DOMContentLoaded", function () {
  const selectAllCheckbox = document.getElementById("select_all");
  const individualCheckboxes = document.querySelectorAll(".select-single");

  selectAllCheckbox.addEventListener("change", function () {
    individualCheckboxes.forEach((checkbox) => {
      checkbox.checked = selectAllCheckbox.checked;
    });
  });

  individualCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      if (!checkbox.checked) {
        selectAllCheckbox.checked = false;
      } else {
        // Verificăm dacă toate celelalte checkbox-uri sunt bifate
        const allChecked = Array.from(individualCheckboxes).every(
          (checkbox) => checkbox.checked
        );
        selectAllCheckbox.checked = allChecked;
      }
    });
  });
});

// Validari
// Validate Login
const validateLoginForm = () => {
  const login = document.getElementById("login").value;
  const password = document.getElementById("password").value;
  let message = "";
  let error = 0;

  if (!/^[A-Za-z0-9]{4,14}$/.test(login)) {
    message += "Login - numai litere și cifre, 4-14 caractere.";
    error++;
  }

  if (!/^[A-Za-z0-9@#$]{6,15}$/.test(password)) {
    if (error > 0) {
      message += "<br/>";
    }
    message += "Parola - numai litere/cifre/@#$, 6-15 caractere.";
    error++;
  }

  const isEmailValid = validateEmail();
  if (!isEmailValid) {
    if (message !== "") {
      message += "<br/>";
    }
    message += document.getElementById("error").innerHTML;
    error++;
  }

  document.getElementById("error").innerHTML = message;

  return error === 0;
};

// Validate Modal
const validateModalForm = () => {
  const contact = document.getElementById("contact").value;
  let message = "";
  let error = 0;

  const isMessageValid = validateMessage();
  if (!isMessageValid) {
    if (message !== "") {
      message += "<br/>";
    }
    message += document.getElementById("error").innerHTML;
    error++;
  }

  if (!/^[A-Za-z0-9.@ ]{1,25}$/.test(contact)) {
    message += "Maxim 25 de caractere, simboluri permise: . @";
    error++;
  }

  document.getElementById("error").innerHTML = message;

  return error === 0;
};

// Validate Contacts
const validateContactForm = () => {
  const name = document.getElementById("name").value;

  let message = "";
  let error = 0;

  if (!/^[A-Za-z0-9 ]{3,25}$/.test(name)) {
    message += "Login - numai litere, cifre și spații, 3-25 caractere.";
    error++;
  }

  const isMessageValid = validateMessage();
  if (!isMessageValid) {
    if (message !== "") {
      message += "<br/>";
    }
    message += document.getElementById("error").innerHTML;
    error++;
  }

  const isEmailValid = validateEmail();
  if (!isEmailValid) {
    if (message !== "") {
      message += "<br/>";
    }
    message += document.getElementById("error").innerHTML;
    error++;
  }

  document.getElementById("error").innerHTML = message;

  return error === 0;
};

// Validate Email
const validateEmail = () => {
  const emails = document.querySelectorAll("#email");
  let message = "";
  let error = 0;
  const emailPattern = /^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/;

  emails.forEach((emailElement) => {
    const email = emailElement.value;
    if (!emailPattern.test(email)) {
      message += `*Fara caractere speciale, respectă șablonul characters@characters.domain<br>`;
      error++;
    }
  });

  document.getElementById("error").innerHTML = message;

  return error === 0;
};

// Validate Message
const validateMessage = () => {
  const texts = document.querySelectorAll("#text");
  let message = "";
  let error = 0;
  const textPattern = /^[a-zA-Z0-9\?\.\:\-, ]{0,150}$/;

  texts.forEach((textElement) => {
    const text = textElement.value;
    if (!textPattern.test(text)) {
      message += `*Mesajul poate conține doar caractere alfanumerice și semne de punctuație (?.,:-)<br>`;
      error++;
    }
  });

  document.getElementById("error").innerHTML = message;

  return error === 0;
};

//Validate CheckBox
const validateCheckBox = () => {
  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
  var checked = false;

  for (let i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      checked = true;
      break;
    }
  }

  if (!checked) {
    alert("Selecteaza cel putin un email inainte de a trimite mesajul");
    return false; // Oprire trimitere
  }

  return true; // Permite trimiterea
};

// Validate Dash Message
const validateMessageDash = () => {
  const texts = document.querySelectorAll("#text");
  let message = "";
  let error = 0;
  const textPattern = /^[a-zA-Z0-9\?\.\(\)\:\-,! ]{0,150}$/;

  texts.forEach((textElement) => {
    const text = textElement.value;
    if (!textPattern.test(text)) {
      message += `*Mesajul poate conține doar caractere alfanumerice și semne de punctuație, fără ghilimele.<br>`;
      error++;
    }
  });

  document.getElementById("error").innerHTML = message;

  return error === 0;
};

// Validate Comment
const validateComment = () => {
  const comments = document.querySelectorAll("#comment");
  let message = "";
  let error = 0;
  const commentPattern = /^[a-zA-Z0-9\?\.\:\-, ]{1,255}$/;

  comments.forEach((commentElement) => {
    const comment = commentElement.value;
    if (!commentPattern.test(comment)) {
      message += `*Comentariul poate conține doar caractere alfanumerice și semne de punctuație (?.,:-).<br>`;
      error++;
    }
  });

  document.getElementById("comment_error").innerHTML = message;

  return error === 0;
};

// send news modal
document.addEventListener("DOMContentLoaded", () => {
  const dialog = document.getElementById("send_dialog");
  const wrapper = document.querySelector(".dialog_wrapper");
  const openModal = document.getElementById("open_modal");
  const openModalSide = document.getElementById("open_modal_sidebar");
  const closeModal = document.getElementById("close_modal");

  openModal.addEventListener("click", () => dialog.showModal());
  openModalSide.addEventListener("click", () => dialog.showModal());

  closeModal.addEventListener("click", () => dialog.close());

  dialog.addEventListener("click", (e) => {
    if (!wrapper.contains(e.target)) {
      dialog.close();
    }
  });
});

// Dashboard Image Zoom
document.addEventListener('DOMContentLoaded', () => {
  const openDialog = (imageSrc) => {
    const dialog = document.getElementById('imageDialog');
    const image = document.getElementById('dialogImage');
    image.src = imageSrc;
    dialog.showModal();
  };

  document.getElementById('imageDialog').addEventListener('click', function () {
    this.close();
  });

  // Facem funcția openDialog disponibilă global
  window.openDialog = openDialog;
});

// validate comment
document.addEventListener("DOMContentLoaded", () => {
  const commentForm = document.getElementById("comment_form");

  commentForm.addEventListener("submit", (event) => {
    const commentTextarea = document.getElementById("comment_textarea");
    const commentError = document.getElementById("comment_error");

    if (commentTextarea.value.trim() === "") {
      commentError.textContent = "Comentariul nu poate fi gol!";
      event.preventDefault();
    } else {
      commentError.textContent = "";
    }
  });
});

// dropdown dashboard
const toggleDropdown = (id) => {
  const dropdowns = document.getElementsByClassName("dropdown-content");
  Array.from(dropdowns).forEach((dropdown) => {
    if (dropdown.id !== id && dropdown.classList.contains("show")) {
      dropdown.classList.remove("show");
    }
  });
  document.getElementById(id).classList.toggle("show");
};

// Închide dropdown-ul dacă utilizatorul face clic în afara acestuia
window.onclick = (event) => {
  if (!event.target.matches(".dropbtn")) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    Array.from(dropdowns).forEach((dropdown) => {
      if (dropdown.classList.contains("show")) {
        dropdown.classList.remove("show");
      }
    });
  }
};
