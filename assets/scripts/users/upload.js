remain.forEach(item => {
  document.getElementById(item).classList.remove("is-hidden")
})

const picInput = document.querySelector('#pic-file input[type=file]');
picInput.onchange = () => {
  if (picInput.files.length > 0) {
    const fileName = document.querySelector('#pic-file .file-name');
    fileName.textContent = picInput.files[0].name;
  }
}

const vidInput = document.querySelector('#vid-file input[type=file]');
vidInput.onchange = () => {
  if (vidInput.files.length > 0) {
    const fileName = document.querySelector('#vid-file .file-name');
    fileName.textContent = vidInput.files[0].name;
  }
}
const quote =  document.getElementById("quote")
const max_characters = 100;
document.getElementById("quote").addEventListener("input", () => {
  let remain_characters = max_characters - quote.value.length;
  document.getElementById("remain_characters").innerHTML = remain_characters
})

document.getElementById("media_submit").addEventListener("click", function(){
  document.getElementById("upload_progress").classList.remove("is-hidden")
})