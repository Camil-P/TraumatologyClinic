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
      .post(SESSION_URL, JSON.stringify(reqData), {
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then(({ data }) => {
        alert("You successfully logged in");
        setCookie(
          "accessToken",
          data?.data.accessToken
          // data.data.accessTokenExpiresIn
        );
        const origin = window.location.origin;

        setCookie("role", data?.data.role);

        if (data.data.role === "Admin") {
          window.location.href = origin + "/pages/Admin/admin.html";
        } else if (data.data.role === "Patient") {
          window.location.href = origin + "/pages/patient/patient.html";
        } else if (data.data.role === "Doctor") {
          window.location.href = origin + "/pages/Doctor/doctor.html";
        }
      })
      .catch(({ response }) => {
        // console.log(response.data.messages[0]);
        alert(response.data.messages[0]);
      });
  },
  false
);
