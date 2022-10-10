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
const mockedData = [
	{
		name: "Nikola",
		surname: "Glisovic",
		email: "dzoniblejz2@gmail.com",
		phoneNumber: 0666406404,
	},
	{
		name: "Nikola",
		surname: "Glisovic",
		email: "dzoniblejz2@gmail.com",
		phoneNumber: 0666406404,
	},
	{
		name: "Nikola",
		surname: "Glisovic",
		email: "dzoniblejz2@gmail.com",
		phoneNumber: 0666406404,
	},
	{
		name: "Nikola",
		surname: "Glisovic",
		email: "dzoniblejz2@gmail.com",
		phoneNumber: 0666406404,
	},
];
const mockedAppointedUser = [
	{
		patient: "Demir Erovic",
		status: "Consultation",
		appointmentDate: "10-10-2022",
		doctor: "Sabir Sagdati",
		actions: { 1: "Further analysis", 2: "Send message" },
	},
	{
		patient: "Demir Erovic",
		status: "Consultation",
		appointmentDate: "10-10-2022",
		doctor: "Sabir Sagdati",
		actions: { 1: "Further analysis", 2: "Send message" },
	},
	{
		patient: "Demir Erovic",
		status: "Consultation",
		appointmentDate: "10-10-2022",
		doctor: "Sabir Sagdati",
		actions: { 1: "Further analysis", 2: "Send message" },
	},
];

const table = document.getElementById("table-patients");
function createTableData() {
	mockedData.forEach((e) => {
		table.innerHTML += `<tbody><tr>
		<td>${e.name}</td>
		<td>${e.surname}</td>
		<td>${e.email}</td>
		<td>${e.phoneNumber}</td>
	</tr>
	</tbody>`;
	});
}
createTableData();

const btnLogout = document.getElementById("logout-doctor");
btnLogout.addEventListener("click", () => {
	deleteCookie("accessToken");
	deleteCookie("role");
	window.location.reload();
});

const appointmentTable = document.getElementById("appointment-table");
function createAppointmentTable() {
	mockedAppointedUser.forEach((e) => {
		appointmentTable.innerHTML += `<tbody>
		<tr>
			<td>${e.patient}</td>
			<td>${e.status}</td>
			<td>${e.appointmentDate}</td>
			<td>${e.doctor}</td>
			<td>${e.actions[1]}</td>
		</tr>
	</tbody>`;
	});
}
createAppointmentTable();
