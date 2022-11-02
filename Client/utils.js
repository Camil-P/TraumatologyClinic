
function getCookie(cookieName) {
  return localStorage.getItem(cookieName);
 
 }
 
 function setCookie(
   key,
   value
   // expireSeconds
   ) {
     localStorage.setItem(key, value);
   }
   
   function deleteCookie(name) {
     localStorage.removeItem(name);
   // setCookie(name, "", null, null, null, 1);
 }
 
 function get_cookie(name) {
   localStorage.getItem(name);
  
 }
 
 
 const formatDate = (dateObj) => {
 let month = dateObj.getUTCMonth() + 1; //months from 1-12
 month = month.toString().length > 1 ? month : "0"+month;
 let day = dateObj.getUTCDate();
 day = day.toString().length > 1 ? day : "0"+day;
 let year = dateObj.getUTCFullYear();
 
 return year + "-" + month + "-" + day;
 };