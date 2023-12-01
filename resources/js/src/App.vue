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

        <div class="sticky top-[100vh] bg-neutral-800">
            <div class="max-w-screen-xl mx-auto px-2 mt-10">
                <footer class="flex flex-col items-center text-white py-8 px-6">
                    <h4 class="mb-3 font-bold text-lg">LUTIKFLO</h4>

                        <p class="mt-4 text-sm font-medium text-neutral-300">Город доставки - Новосибирск</p>
                        <p class="mt-4 text-sm font-medium text-neutral-300">Адрес магазина: Ипподромская 46</p>
                        <p class="mt-4 mb-4 text-sm font-medium  text-neutral-300">Работаем ежедневно с 10:00 до 21:00</p>
                        <div class="flex">
                            <span class="mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
</svg>

                            </span>
                            <a class="font-medium" href="tel:+79833064216">+7(983)306-42-16</a>
                        </div>



                </footer>
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
