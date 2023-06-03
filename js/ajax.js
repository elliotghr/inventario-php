const fromSubmit = async (e) => {
  let $form = document.querySelector(".FormularioAjax");

  let formData = new FormData($form);
  const action = e.target.getAttribute("action");
  const options = {
    method: "POST",
    body: formData,
  };

  try {
    let res = await fetch(action, options);
    if (!res.ok) throw { status: res.status, statusText: res.statusText };
    let json = await res.text();
    document.querySelector(".form-rest").innerHTML = json;
  } catch (error) {
    console.log(error);
  }
};

document.addEventListener("submit", async (e) => {
  if (e.target.matches(".FormularioAjax")) {
    e.preventDefault();
    let isConfirm = confirm("Desea enviar el formulario?");
    if (!isConfirm) return;

    fromSubmit(e);
  }
});
