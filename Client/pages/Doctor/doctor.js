const token = getCookie("accessToken");

var dateFormField = document.getElementById("date");
dateFormField.value = formatDate(new Date());
const startingHourFormField = document.getElementById("startingHour");

var appointmentsRes = [];
var patientList = [];
var messageData = {};
var selectedChatPerson = null;

const doctorCancelAppointment = (id) => {
  axios
    .delete(APPOINTMENT_URL + "?appointmentId=" + id, {
      headers: {
        Authorization: token,
      },
    })
    .then((res) => {
      alert("Appointment deleted successfully.");
      window.location.reload();
    })
    .catch((err) => {
      console.log(err);
      alert(err);
    });
};

const myFunction = () => {
  document.getElementById("myDropdown").classList.toggle("show");
};

window.onclick = function (event) {
  if (!event.target.matches(".dropbtn")) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains("show")) {
        openDropdown.classList.remove("show");
      }
    }
  }
};
const onSelectChange = () => {
  let patient = document.getElementById("patient-list");
  d = document.getElementById("select_id").value;
  showSelectedContent(d);
  if (d === "patients") {
    patient.classList.add("random_test");
  }
};

const showSelectedContent = (activeContent) => {
  const container = document.getElementById("container-content");
  for (content of container.children) {
    if (content.id === activeContent) {
      content.style.display = "flex";
    } else {
      content.style.display = "none";
    }
  }
};

const displayStartingHours = (date) => {
  startingHourFormField.innerHTML = "";

  let appointmentStartingHours = appointmentsRes
    .filter((a) => a.date === date.toString())
    .map((a) => a.startingHour);


  for (let i = 8; i < 16; i++) {
    const optionEl = document.createElement("option");
    optionEl.value = i;
    if (appointmentStartingHours.includes(i)) {
      optionEl.innerHTML = i + " Already appointed";
      optionEl.disabled = true;
	  optionEl.style['color'] = 'white !important';
    } else {
      optionEl.innerHTML = i;
    }
    startingHourFormField.appendChild(optionEl);
  }
};

const handleDateChange = (dateEl) => {
  const dateVal = dateEl.value;
  // console.log(dateEl.value);
  displayStartingHours(dateVal);
};

const getAppointments = async () => {
  res = await axios
    .get(APPOINTMENT_URL, {
      headers: {
        Authorization: token,
      },
    })
    .then((res) => {
      appointmentsRes = res.data.data;
      createAppointmentTable(appointmentsRes);
      displayStartingHours(dateFormField.value);
    })
    .catch((err) => {
      console.log(err);
    });
};

getAppointments();

const form = document.getElementById("createAppointment");
form.addEventListener(
  "submit",
  async (event) => {
    event.preventDefault();

	const valuesToParse = ['startingHour', 'patientId']; 

    const formData = new FormData(form);
    const reqData = {};
    for (var [key, value] of formData.entries()) {
      reqData[key] = valuesToParse.includes(key) ? parseInt(value) : value;
    }
	console.log(reqData);

    axios
      .post(APPOINTMENT_URL, JSON.stringify(reqData), {
        headers: {
          "Content-Type": "application/json",
          Authorization: token,
        },
      })
      .then((res) => {
        alert("Appointment created successfully.");
        window.location.reload();
      })
      .catch(({ response }) => {
        console.log(response.data.messages[0]);
        alert(response.data.messages[0]);
      });
  },
  false
);

const table = document.getElementById("table-patients");
function createTableDataPatients(appArr) {
  appArr.forEach((e) => {
    table.innerHTML += `<tbody><tr>
		<td></td>
		<td>${e.surname}</td>
		<td>${e.email}</td>
		<td>${e.phoneNumber}</td>
	</tr>
	</tbody>`;
  });
}

const btnLogout = document.getElementById("logout-doctor");
btnLogout.addEventListener("click", () => {
  deleteCookie("accessToken");
  deleteCookie("role");
  window.location.reload();
});

const appointmentTable = document.getElementById("appointment-table");
function createAppointmentTable(appointmentsRes) {
  // data,serviceName,startingHour,completionStatus

  appointmentsRes.forEach((e) => {
    appointmentTable.innerHTML += `<tbody>
		<tr>
			<td>${e.date}</td>
			<td>${e.serviceName}</td>
			<td>${e.startingHour} h</td>
			<td>${e.completionStatus}</td>
			<td>${e.name} ${e.surname}</td>
			${
        new Date(e.date).getTime() >= new Date().getTime()
          ? `<td class="cancel-app" onclick="DoctorcancelAppointment(${e.id})">Cancel</td>`
          : ``
      }
		</tr>
	</tbody>`;
  });
}

function loadCreateAppointmentsPatients() {
  const patientsSelect = document.getElementById("patientsSelect");

  patientsSelect.innerHTML = "";

  patientList.forEach(
    (p) =>{
	  console.log(p);
      (patientsSelect.innerHTML += `<option value="${p.patientId}">${p.name} ${p.surname}</option>`)}
  );
}

const fetchPatients = () => {
  axios
    .get(DOCTOR_CONTROLLER + "?fetch=patients", {
      headers: {
        Authorization: token,
      },
    })
    .then((res) => {
      patientList = res.data.data;
      getPatient(patientList);
      loadChatPersons();

      loadCreateAppointmentsPatients(patientList);
      changeSelectedChatPerson(patientList[0].id);
    })
    .catch((err) => {
      console.log(err);
      // alert(err);
      // throw err;
    });
};

fetchPatients();

const patient = document.getElementById("table-patients");
function getPatient(arr) {
  arr.forEach((e) => {
    patient.innerHTML += `<tbody>
		<tr>
			<td>${e.name}</td>
			<td>${e.surname}</td>
			<td>${e.email}</td>
			<td>${e.phoneNumber}</td>
		</tr>
	</tbody>`;
  });
}

// MESSAGES LOGIC

function loadChatPersons() {
  const messagedPersonsContainer = document.getElementById("messagedPersons");
  messagedPersonsContainer.innerHTML = "";

  patientList.forEach(
    (cp) =>
      (messagedPersonsContainer.innerHTML += `<button id="p${
        cp.id
      }" onClick="changeSelectedChatPerson(${cp.id})" class="chatPerson">${
        cp.name + " " + cp.surname
      }</button>`)
  );
}

function changeSelectedChatPerson(id) {
  if (selectedChatPerson) {
    document.getElementById("p" + selectedChatPerson).style[
      "background-color"
    ] = "rgb(7, 171, 116)";
  }

  selectedChatPerson = id;

  document.getElementById("p" + selectedChatPerson).style["background-color"] =
    "rgb(171, 7, 21)";

  fetchMessages();
}

function fetchMessages() {
  axios
    .get(MESSAGE_CONTROLLER, {
      headers: {
        Authorization: token,
      },
    })
    .then(({ data }) => {
      messageData = data.data;
      loadMessages();
    })
    .catch(({ response }) => {
      console.log(response.data, "fetchAppointments");
    });
}

function loadMessages() {
  const messageContainer = document.getElementById("displayedMessages");
  messageContainer.innerHTML = "";

  messageData.messages
    .filter(
      (lm) =>
        lm.receiver === selectedChatPerson || lm.sender === selectedChatPerson
    )
    .forEach((fm) => addMessage(messageContainer, fm.content, fm.receiver));

  messageContainer.scrollTop = messageContainer.scrollHeight;
}

function addMessage(messageContainer, content, receiver) {
  messageContainer.innerHTML +=
    receiver === selectedChatPerson
      ? `<h1 class="sentMessage">${content}</h1>`
      : `<h1 class="receivedMessage">${content}</h1>`;
}

function sendMessage() {
  const chatInputData = document.getElementById("chatInput");

  axios
    .post(
      MESSAGE_CONTROLLER,
      { receiver: selectedChatPerson, content: chatInputData.value },
      {
        headers: {
          "Content-Type": "application/json",
          Authorization: token,
        },
      }
    )
    .then(({ data }) => {
      const messageContainer = document.getElementById("displayedMessages");

      console.log(data);
      addMessage(messageContainer, data.data.content, data.data.receiver);
      chatInputData.value = "";
    })
    .catch(({ response }) => {
      console.log(response);
      alert(response.data.messages[0]);
    });
}
