var dateFormField = document.getElementById("date");
dateFormField.value = formatDate(new Date());
const startingHourFormField = document.getElementById("startingHour");

const token = getCookie("accessToken");

let appointments = [];

const fetchDoctors = () => {
  axios
    .get(
      "http://localhost/Clinic/Api/controllers/PatientController.php?fetch=doctors",
      {
        headers: {
          Authorization: token,
        },
      }
    )
    .then((res) => {
      console.log(res);
      const doctorsList = res.data.data;
      //sort by your selected doctor
      const falseFirst = doctorsList.sort((a, b) => Number(b.assigned) - Number(a.assigned));
      createDoctorsList(falseFirst)
    })
    .catch((err) => {
      console.log(err);
    });
};

fetchDoctors();

function createDoctorsList(listDoctors) {
  const doctorContainer = document.getElementById("doctors-container");
  
  listDoctors.forEach((doctor) => {
    doctorContainer.innerHTML += `<div class="doctors-card">
    <div class="doctors-image">
      <div class="image-doctors">
        <img class="img-doctor"
          src="https://cdn-prod.medicalnewstoday.com/content/images/articles/317/317991/doctor-in-branding-article.jpg"
          alt="doctors-card" />
      </div>
    </div>
    <div class="doctors-info">
      <div class="doctors-info-container">
        <h2>Doctor Name: ${doctor.name}</h2>
        <h2>Surname:${doctor.surname }</h2>
        <h2>Email: ${doctor.email} </h2>
        <h2>Gender:${doctor.gender}</h2>
        <h2>Phone number:${doctor.phoneNumber}</h2>
        <h2>Birth Place: ${doctor.birthPlace}</h2>
      </div>
    </div>
    <div class="doctors-select">
      <div class="button-container">
        ${doctor.assigned ?
          `<button disabled class="btn-doctor-false">Your selected doctor</button>` :
          `<button onclick="requestChange(${doctor.id})" id="${"d" + doctor.id}" class="btn-doctor">Request to change your doctor</button>`}
        
        <button class="btn-doctor">Send message</button>
      </div>
    </div>
    </div>`;
  });
}

const fetchAppointments = () => {
  axios
    .get("http://localhost/Clinic/Api/controllers/AppointmentController.php", {
      headers: {
        Authorization: token,
      },
    })
    .then(({ data }) => {
      appointments = data.data;
      console.log(appointments,"fetchAppRes");
      displayStartingHours(dateFormField.value);
      displayUpcomingAppointments(appointments);
    })
    .catch(({ response }) => {
      console.log(response.data,"fetchAppointments");
    });
};

fetchAppointments();

const requestChange = (id) => {
  axios
  .post("http://localhost/Clinic/Api/controllers/PatientController.php", 
  JSON.stringify({ requestedDoctorsId: id }),
  {
    headers: {
      "Content-Type": "application/json",
      Authorization: token,
    }
  })
  .then(({ data }) => {
    console.log(data);
    alert("Successfully created doctor change request!");
  })
  .catch(({ response }) => {
    console.log(response);
    alert(response.data.messages[0]);
  });
}

const logoutBtn = document.querySelector("#logout-patient");

logoutBtn?.addEventListener("click", (el) => {
  el.preventDefault();

  deleteCookie("accessToken");
  deleteCookie("role");
  window.location.href = "http://127.0.0.1:5500/Client/index.html";
});

const modalProfile = document.getElementsByClassName("modal-profile")[0];
const btn_profile = document.getElementById("profile-btn");
const btnClose = document.getElementById("close-modal");

btn_profile.addEventListener("click", (el) => {
  axios
  .get(
    "http://localhost/Clinic/Api/controllers/PatientController.php?fetch=profile",
    {
      headers: {
        Authorization: token,
      },
    }
  )
  .then(({ data }) => {
    modalProfile.style.display = "block";
    document.getElementById('profileName').innerHTML += data.data.name;
    document.getElementById('profileSurname').innerHTML += data.data.surname;
    document.getElementById('profileEmail').innerHTML += data.data.email;
    document.getElementById('profileBirthDate').innerHTML += data.data.birthDate;
    document.getElementById('profileBirthPlace').innerHTML += data.data.birthPlace;
    document.getElementById('profilePhoneNumber').innerHTML += data.data.phoneNumber;
    document.getElementById('profileGender').innerHTML += data.data.gender;
  })
  .catch((err) => {
    alert(err.response.data.messages[0]);
    console.log(err);
  });
});

btnClose.addEventListener("click", () => {
  modalProfile.style.display = "none";
});

const form = document.getElementById("createAppointment");
form.addEventListener(
  "submit",
  async (event) => {
    event.preventDefault();

    const formData = new FormData(form);
    const reqData = {};
    for (var [key, value] of formData.entries()) {
      reqData[key] = key === "startingHour" ? parseInt(value) : value;
    }

  axios
      .post(
        "http://localhost/Clinic/Api/controllers/AppointmentController.php",
        JSON.stringify(reqData),
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: token,
          },
        }
      )
      .then((res) => {
        alert("Appointment created successfully.");
        window.location.reload();
      })
      .catch((err) => {
        console.log(err)
        alert(err);
      });
  },
  false
);

const handleDateChange = (dateEl) => {
  const dateVal = dateEl.value
  // console.log(dateEl.value);
  displayStartingHours(dateVal);
};

const displayStartingHours = (date) => {
  startingHourFormField.innerHTML = "";

  let appointmentStartingHours = appointments
    .filter((a) => a.date === date.toString())
    .map((a) => a.startingHour);

  for (let i = 8; i < 16; i++) {
    const optionEl = document.createElement('option');
    optionEl.value = i;
    if (appointmentStartingHours.includes(i)){
      optionEl.innerHTML = i + " Already appointed";
      optionEl.disabled = true;
    }
    else{
      optionEl.innerHTML = i;
    }
      startingHourFormField.appendChild(optionEl);
  }
};

const displayUpcomingAppointments = (appointments) => {
  const upcomingAppointmentsContainer = document.getElementById("upcomingAppointments");
  appointments.filter(a => new Date(a.date).getTime() >= new Date().getTime()).forEach(a => {
    upcomingAppointmentsContainer.innerHTML += `
    <div>
      <h1>${a.serviceName}</h1>
      <h1>${a.date} ${a.startingHour}h</h1>
      <button onclick="cancelAppointment(${a.id})">cancel <br> appointment</button>
    </div>`;
  });
}

const cancelAppointment = (id) => {
  axios.delete(
    "http://localhost/Clinic/Api/controllers/AppointmentController.php?appointmentId="+id,
    {
      headers: {
        Authorization: token,
      },
    }
  )
  .then((res) => {
    alert("Appointment deleted successfully.");
    window.location.reload();
  })
  .catch((err) => {
    console.log(err)
    alert(err);
  });
}