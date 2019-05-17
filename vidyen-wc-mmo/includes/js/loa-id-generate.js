//Sets the field to copy link

function copy_link()
{
  var copyText = document.getElementById("url_output");
  copyText.select();
  document.execCommand("copy");
  alert("Paste this command into LoA: " + copyText.value);
}
