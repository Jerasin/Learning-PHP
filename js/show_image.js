const showImage = () => {
  const id_image = document.getElementById("id_image");
  const image = document.getElementById("blah");
  const [file] = id_image.files;
  if (file) {
    image.src = URL.createObjectURL(file);
  }
};
