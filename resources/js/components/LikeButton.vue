<template>
    <div>
        <button type="submit" value="" id="likedSongButton" @click="likeSong">
            <img id="likedSongImage" src="">
        </button>
    </div>
</template>

<script>
    export default {
        methods: {
            likeSong() {
                console.log("like song button clicked");
            }
        }
    }
</script>