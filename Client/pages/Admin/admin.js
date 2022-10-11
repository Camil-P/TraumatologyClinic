const logoutBtn = document.querySelector("#logout-doctor");

logoutBtn?.addEventListener("click", (el) => {
  el.preventDefault();

  deleteCookie("accessToken");
  deleteCookie("role");
  window.location.href = "http://127.0.0.1:5500/Client/index.html";
});

const modalProfile = document.getElementsByClassName(
  "container-register-doctor"
)[0];
const btn_profile = document.getElementById("add-btn");
const btnClose = document.getElementById("close-modal");

btn_profile.addEventListener("click", (el) => {
  modalProfile.style.display = "block";
});

btnClose.addEventListener("click", () => {
  modalProfile.style.display = "none";
});

//create dynamic table for doctors !!! ADMIN PAGE

const tableHeaders = document.getElementById("doctorsTable");
const tablePatientHeaders = document.getElementById("patientsTable");
console.log(tablePatientHeaders);

function createDynamicTable(listDoctors) {
  listDoctors.forEach((doctor) => {
    let tr = document.createElement("tr");
    tableHeaders.append(tr);

    const tdName = document.createElement("td");
    const tdSurname = document.createElement("td");
    const tdEmail = document.createElement("td");
    const tdPhoneNumber = document.createElement("td");
    tr.append(tdName);
    tr.append(tdSurname);
    tr.append(tdEmail);
    tr.append(tdPhoneNumber);
    tdName.innerHTML = doctor.name;
    tdSurname.innerHTML = doctor.surname;
    tdEmail.innerHTML = doctor.email;
    tdPhoneNumber.innerHTML = doctor.phoneNumber;
  });
}

function createDynamicPatientTable(patientList) {
  console.log(patientList, "Dwadwa");

  patientList.forEach((patient) => {
    console.log(patient);
    let tr = document.createElement("tr");
    tablePatientHeaders.append(tr);

    const tdName = document.createElement("td");
    const tdSurname = document.createElement("td");
    const tdEmail = document.createElement("td");
    const tdPhoneNumber = document.createElement("td");
    const tdPatientID = document.createElement("td");
    const submitReq = document.createElement("button");
    const cancelReq = document.createElement("button");

    // const tdReqDoctorID = document.createElement("td");

    tr.append(tdName);
    tr.append(tdSurname);
    tr.append(tdEmail);
    tr.append(tdPhoneNumber);
    tr.append(tdPatientID);
    // tr.append(tdReqDoctorID);
    
    tdName.innerHTML = patient.name;
    tdSurname.innerHTML = patient.surname;
    tdEmail.innerHTML = patient.email;
    tdPhoneNumber.innerHTML = patient.phoneNumber;
    if(patient.requests){
      
      tr.append(submitReq);
      tr.append(cancelReq)
      tdPatientID.innerHTML = `PatientID: ${patient.requests.PatientId}  PrevDoctor:${patient.requests.PreviouseDoctorId}  RequestDoctor:${patient.requests.RequestDoctorId}`
      submitReq.innerHTML= "Approve"
      cancelReq.innerHTML="Cancel"
      cancelReq.classList.add("cancel-btn");
      cancelReq.addEventListener('click',()=> {
        console.log(patient.requests.Id)
        axios.delete(
          "http://localhost/Clinic/Api/controllers/AdminController.php?requestId="+patient.requests.Id,
          {
            headers: {
              Authorization: token,
            },
          }
        )
        .then((res) => {
          console.log(res,"CAO")
          alert("Requests approved");
          window.location.reload();
        })
        .catch((err) => {
          console.log(err)
          alert(err);
        });
      })



      submitReq.classList.add("approve-btn")

      submitReq.addEventListener('click',()=> {
        console.log(patient.requests.Id)
        axios.patch(
          "http://localhost/Clinic/Api/controllers/AdminController.php?requestId="+patient.requests.Id,null,
          {
            headers: {
              Authorization: token,
            },
          }
        )
        .then((res) => {
          console.log(res,"CAO")
          alert("Requests approved");
          // window.location.reload();
        })
        .catch((err) => {
          console.log(err)
          alert(err);
        });
      })

      //dwadwa
    }
  });

 
 
}
// createDynamicTable(listDoctors);

//Created user from Role
const token = getCookie("accessToken");

const form = document.getElementById("registerFromAdmin");
form.addEventListener(
  "submit",
  async (event) => {
    event.preventDefault();
    const formData = new FormData(form);
    const reqData = {};
    for (var [key, value] of formData.entries()) {
      reqData[key] = value;
    }
    await axios
      .post(
        "http://localhost/Clinic/Api/controllers/AdminController.php",
        JSON.stringify(reqData),
        {
          headers: {
            Authorization: token,
            "Content-Type": "application/json",
          },
        }
      )
      .then((res) => {
        console.log(res);
        alert("You have successfully created an account");
        window.location.reload();
        modalProfile.style.display = "none";
      })
      .catch(({ response }) => {
        console.log(response);
        // console.log(response.data);
        // alert(response.data.messages[0]);
        // const messageErr = err.response.data.messages

        // alert(`Creating not successfully:  ${messageErr}`);
        // throw err;
      });
  },
  false
);

const fetchDoctors = () => {
  console.log(token);
  axios
    .get(
      "http://localhost/Clinic/Api/controllers/AdminController.php?fetch=doctors",
      {
        headers: {
          Authorization: token,
        },
      }
    )
    .then((res) => {
      console.log(res);
      const doctorsList = res.data.data;
      console.log(doctorsList);
      createDynamicTable(doctorsList);
    })
    .catch((err) => {
      console.log(err);
      // alert(err);
      // throw err;
    });
};

fetchDoctors();

const fetchPatients = () => {
  console.log(token, "tu sma i ja");
  axios
    .get(
      "http://localhost/Clinic/Api/controllers/AdminController.php?fetch=patients",
      {
        headers: {
          Authorization: token,
        },
      }
    )
    .then((res) => {
      console.log("patientRes: " + res);
      const patientList = res.data.data;
      console.log(patientList, "tuuu");
      createDynamicPatientTable(patientList);
    })
    .catch((err) => {
      console.log(err);
      // alert(err);
      // throw err;
    });
};

fetchPatients();



