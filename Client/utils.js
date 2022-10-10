
// const setCookie = (cookieName, value, maxAge) => {
//   console.log(cookieName, value, maxAge);
//   console.log(maxAge ? "max-age=" + maxAge : "");
//   document.cookie = `${cookieName}=${value};${
//     maxAge ? "max-age=" + maxAge : ""
//   };path=/`;
// };

// const clearCookie = (cookieName) => {
//     const expireDate = new Date();
//     document.cookie = `${cookieName}="";expires=${
//         expireDate.getSeconds() + 5
//     };path=/`;

//     console.log(expireDate)
//   console.log(document.cookie)
// };

function setCookie(
  key,
  value,
  expireSeconds
  ) {
  var expireDate = new Date();
  
  if (expireSeconds) {
    expireDate.setSeconds(expireDate.getSeconds() + expireSeconds);
  }
  document.cookie =
    key +
    "=" +
    value +
    ";domain=" +
    window.location.hostname +
    ";path=/" +
    ";expires=" +
    expireDate.toUTCString();
  }
  
  function deleteCookie(name) {
  setCookie(name, "", null, null, null, 1);
}

function get_cookie(name) {
  return document.cookie.split(";").some((c) => {
    return c.trim().startsWith(name + "=");
  });
}

const getCookie = (cookieName) => {
  return document.cookie
    .split(";")
    .find((row) => row.includes(cookieName + "="))
    ?.replace(cookieName+"=", "");
};

const formatDate = (dateObj) => {
let month = dateObj.getUTCMonth() + 1; //months from 1-12
month = month.toString().length > 1 ? month : "0"+month;
let day = dateObj.getUTCDate();
day = day.toString().length > 1 ? day : "0"+day;
let year = dateObj.getUTCFullYear();

return year + "-" + month + "-" + day;
};