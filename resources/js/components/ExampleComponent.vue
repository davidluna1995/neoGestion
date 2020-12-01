<template >

    <div style="background:#E5E7E9" >

        <transition name="">
           <div> <router-view></router-view></div>
        </transition>

    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
