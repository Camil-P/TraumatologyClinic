const token = getCookie('accessToken');

const DoctorcancelAppointment = (id) => {
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



const getAppointments = async () => {
	console.log(token)
	res = await axios
	  .get(
		"http://localhost/Clinic/Api/controllers/AppointmentController.php",
		{
		  headers: {
			Authorization: token,
		  },
		}
	  )
	  .then((res) => {
		console.log(res);
		const appointmentsRes = res.data.data;
		createAppointmentTable(appointmentsRes)
	  })
	  .catch((err) => {
		console.log(err);
		
	  });
  };


 getAppointments();


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
			<td>${e.startingHour}</td>
			<td>${e.completionStatus}</td>
			<td>${e.id}</td>
			<td class="cancel-app" onclick="DoctorcancelAppointment(${e.id})">Cancel</td>
		</tr>
	</tbody>`;
	});
}




const fetchPatients = () => {
	console.log(token, "tu sma i ja");
	axios
	  .get(
		"http://localhost/Clinic/Api/controllers/DoctorController.php?fetch=patients",
		{
		  headers: {
			Authorization: token,
		  },
		}
	  )
	  .then((res) => {
		const patientList = res.data.data;
		console.log(patientList,"tuu")
		getPatient(patientList)
	  })
	  .catch((err) => {
		console.log(err);
		// alert(err);
		// throw err;
	  });
  };
  
  fetchPatients();

  const patient = document.getElementById('table-patients')
  function getPatient(arr){
	
	arr.forEach((e) => {
		
		patient.innerHTML+=	`<tbody>
		<tr>
			<td>${e.name}</td>
			<td>${e.surname}</td>
			<td>${e.email}</td>
			<td>${e.phoneNumber}</td>
		</tr>
	</tbody>`;
	})
  }