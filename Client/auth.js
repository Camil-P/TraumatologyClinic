(function validation() {
  const accessToken = getCookie("accessToken");
  const role = getCookie("role");

  const currentHref = window.location.href;
  const origin = window.location.origin;

  if (!accessToken || accessToken === "") {
    if (currentHref !== origin+"/pages/login/login.html" &&
        currentHref !== origin+"/index.html" &&
        currentHref !== origin+"/pages/register/register.html")
      window.location.href = origin+"/index.html";
  } else{
    switch (role) {
      case "Admin":
        if (currentHref != origin+"/pages/Admin/admin.html")
          window.location.href = origin+"/pages/Admin/admin.html";
        break;
      case "Patient":
        if (currentHref != origin+"/pages/patient/patient.html")
          window.location.href = origin+"/pages/patient/patient.html";

        break;
      case "Doctor":
        if (currentHref != origin+"/pages/Doctor/doctor.html")
          window.location.href = origin+"/pages/Doctor/doctor.html";

        break;
      default:
        break;
    }
  }
})();
