(function validation() {
  const accessToken = getCookie("accessToken");
  const role = getCookie("role");

  const currentHref = window.location.href;

  if (!accessToken || accessToken === "") {
    if (currentHref !== "http://127.0.0.1:5500/Client/pages/login/login.html" &&
        currentHref !== "http://127.0.0.1:5500/Client/pages/register/register.html")
      window.location.href = "http://127.0.0.1:5500/Client/index.html";
  } else{
    switch (role) {
      case "Admin":
        if (currentHref != "http://127.0.0.1:5500/Client/pages/Admin/admin.html")
          window.location.href = "http://127.0.0.1:5500/Client/pages/Admin/admin.html";
        break;
      case "Patient":
        if (currentHref != "http://127.0.0.1:5500/Client/pages/patient/patient.html")
          window.location.href = "http://127.0.0.1:5500/Client/pages/patient/patient.html";

        break;
      case "Doctor":
        if (currentHref != "http://127.0.0.1:5500/Client/pages/Doctor/doctor.html")
          window.location.href = "http://127.0.0.1:5500/Client/pages/Doctor/doctor.html";

        break;
      default:
        break;
    }
  }
})();
