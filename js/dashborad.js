// Listen for click on toggle checkbox
$("#select_all").on("click", function (event) {
  if (this.checked) {
    // Iterate each checkbox
    $(":checkbox").each(function () {
      this.checked = true;
    });
  } else {
    $(":checkbox").each(function () {
      this.checked = false;
    });
  }
  // console.log("object");
});
