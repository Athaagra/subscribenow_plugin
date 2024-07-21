/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************!*\
  !*** ./src/subscribenow.js ***!
  \*****************************/
wp.blocks.registerBlockType("ourdatabaseplugin/subscribenow", {
  title: "Subscribe Now",
  edit: function () {
    return; // Since JavaScript does not handle server-side actions directly like PHP,
    // you would typically use AJAX to send the form data to the server.
    // Below is an example using vanilla JavaScript to send the form data.

    document.addEventListener("DOMContentLoaded", function () {
      const form = document.querySelector('.create-sub-form');
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        formData.append('action', 'createsubscriber'); // Set any additional data

        fetch(admin_url('admin-post.php'), {
          // Replace 'admin_url' with the actual admin URL
          method: 'POST',
          body: formData
        }).then(response => response.json()).then(data => {
          console.log(data); // Handle the response data
        }).catch(error => {
          console.error('Error:', error); // Handle any errors
        });
      });
    });
  },
  save: function () {
    return null;
  }
});
/******/ })()
;
//# sourceMappingURL=subscribenow.js.map