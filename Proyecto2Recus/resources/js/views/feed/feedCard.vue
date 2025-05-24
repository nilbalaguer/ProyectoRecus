<template>
    <div class="feed-card feed-card-gradient-4">
        <div class="feed-card-pfp" :style="{ backgroundImage: 'url(/images/' + pfp + ')' }"></div>
        <h2>{{ title }}</h2>
        <p>{{ description }}</p>
    </div>
</template>

<script>
export default {
  props: {
    pfp: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    }
  }
};
</script>
