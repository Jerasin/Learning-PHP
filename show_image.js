id_image.onchange = (e) => {
  const [file] = id_image.files;
  if (file) {
    blah.src = URL.createObjectURL(file);
  }
};
