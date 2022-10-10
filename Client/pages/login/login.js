const form = document.getElementById("loginForm");
form.addEventListener(
  "submit",
  async (event) => {
    event.preventDefault();

    const formData = new FormData(form);
    const reqData = {};
    for (var [key, value] of formData.entries()) {
      reqData[key] = value;
    }

    res = await axios
      .post(
        "http://localhost/Clinic/Api/controllers/SessionController.php",
        JSON.stringify(reqData),
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
      .then(({ data }) => {
        alert("You successfully logged in");
        setCookie(
          "accessToken",
          data?.data.accessToken,
          data.data.accessTokenExpiresIn
        );
        setCookie("role", data?.data.role, data.data.accessTokenExpiresIn);
        if (data.data.role === "Admin") {
          window.location.href =
            "http://127.0.0.1:5500/Client/pages/Admin/admin.html";
        } else if (data.data.role === "Patient") {
          window.location.href =
            "http://127.0.0.1:5500/Client/pages/patient/patient.html";
        } else if (data.data.role === "Doctor") {
          window.location.href =
            "http://127.0.0.1:5500/Client/pages/Doctor/doctor.html";
        }
      })
      .catch((err) => {
        console.log(err);
        alert("Login failed");
      });
  },
  false
);
