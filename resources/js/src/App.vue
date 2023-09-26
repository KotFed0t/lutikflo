<template>
    <div class="app">
        <navBar></navBar>
        <router-view/>
    </div>
</template>

<script>
import NavBar from "./components/navBar.vue";
export default {
    name: "App",
    components: {NavBar}
}
</script>

<style scoped>
.app {
    padding: 30px;
}

</style>
