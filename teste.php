<script>
console.log("UA:", navigator.userAgent);
console.log("speechSynthesis in window:", "speechSynthesis" in window);
console.log("SpeechSynthesisUtterance in window:", "SpeechSynthesisUtterance" in window);

if ("speechSynthesis" in window) {
  const v = speechSynthesis.getVoices();
  console.log("voices length:", v.length);
  console.log(v.map(x => ({name:x.name, lang:x.lang, local:x.localService})));
  speechSynthesis.onvoiceschanged = () => {
    const vv = speechSynthesis.getVoices();
    console.log("voiceschanged ->", vv.length);
  };
}	
</script>