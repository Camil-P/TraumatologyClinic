(function validation() {
  const accessToken = getCookie("accessToken");
  const role = getCookie("role");

  if (!accessToken || accessToken === "") {
    window.location.href = "http://127.0.0.1:5500/Client/index.html";
  } else{
    switch (role) {
      case "Admin":
        "http://127.0.0.1:5500/Client/pages/Admin/admin.html";
        break;
      case "Patient":
        "http://127.0.0.1:5500/Client/pages/patient/patient.html";

        break;
      case "Doctor":
        "http://127.0.0.1:5500/Client/pages/Doctor/doctor.html";

        break;
      default:
        break;
    }
  }
})();
