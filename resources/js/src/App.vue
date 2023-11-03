<template>
    <div class="app">
        <navBar></navBar>
        <Categories></Categories>
        <hr>
        <router-view/>
    </div>
</template>

<script>
import NavBar from "./components/navBar.vue";
import Categories from "./components/Categories.vue";
export default {
    name: "App",
    components: {Categories, NavBar}
}
</script>

<style scoped>
.app {
    padding: 30px;
}

</style>
