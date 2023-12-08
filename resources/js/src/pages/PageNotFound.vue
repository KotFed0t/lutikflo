<template>
    <div class="text-center mt-24">
        <p class="font-medium text-xl">Страница не найдена...</p>
        <button class="bg-black text-white rounded-full px-4 py-1.5 hover:bg-neutral-700 mt-5" @click="$router.push({name: 'home'})">На главную</button>

    </div>
</template>

<script>
export default {
    name: "PageNotFound"
}
</script>

<style scoped>

</style>
