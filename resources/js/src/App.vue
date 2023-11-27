<template>
    <div class="app max-w-screen-xl mx-auto  min-h-screen px-8">
        <navBar></navBar>
        <Categories></Categories>
        <hr>
        <router-view/>
        <foot class="sticky top-[100vh]">Footer</foot>
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

</style>
