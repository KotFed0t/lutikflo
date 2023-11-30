<template>
    <!--    <div class="app max-w-screen-xl mx-auto min-h-screen px-8">-->
    <div class="min-h-screen">
        <div class="">
            <div class="max-w-screen-xl mx-auto px-2">
                <navBar></navBar>
            </div>
        </div>

        <div class="">
            <div class="max-w-screen-xl mx-auto px-2">
                <Categories></Categories>
            </div>
        </div>

        <div class="">
            <div class="max-w-screen-xl mx-auto px-2">
                <router-view/>
            </div>
        </div>

        <div class="sticky top-[100vh] bg-gray-950">
            <div class="max-w-screen-xl mx-auto px-2 mt-5">
                <footer class="text-white">Footer</footer>
            </div>
        </div>

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
