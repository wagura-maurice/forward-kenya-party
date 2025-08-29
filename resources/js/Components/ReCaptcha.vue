<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
  action: {
    type: String,
    required: true
  },
  siteKey: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['captcha-response']);
const captchaResponse = ref(null);

onMounted(() => {
  const script = document.createElement('script');
  script.src = `https://www.google.com/recaptcha/api.js?render=${props.siteKey}`;
  document.head.appendChild(script);

  script.onload = () => {
    window.grecaptcha.ready(() => {
      window.grecaptcha.execute(props.siteKey, { action: props.action })
        .then(token => {
          captchaResponse.value = token;
          emit('captcha-response', token);
        });
    });
  };
});
</script>

<template>
  <input type="hidden" name="g-recaptcha-response" :value="captchaResponse">
</template>