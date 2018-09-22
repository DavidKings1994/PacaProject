<template id="">
    <transition name="fade">
        <div class="character_card_background" v-if="selectedId != null">
            <transition name="card-enter">
                <div class="characterCard" v-if="selectedId != null">
                    <div class="characterCardBody">
                        <h4 class="head">ID</h4>
                        <h5>nombre</h5>
                        <div style="height:100%;">
                            <img :src="characters[selectedId].image" alt="character">
                            <img src="/assets/character_card_foreground.png" alt="foreground" class="character_card_background">
                        </div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                        <p>traits</p>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script type="text/javascript">
export default {
    data: function() {
        return {
            open: false,
            index: 0
        }
    },
    props: ['characters', 'selectedId'],
    watch: {
        selectedId: function() {
            console.log(this.selectedId);
            this.index = this.selectedId;
        }
    },
    methods: {
        close: function() {
            this.open = false;
        }
    },
    mounted: function() {
        console.log(this.selectedId);
        // window.eventBus.$emit('updateCharacters');
    }
}
</script>

<style media="screen">
    .character_card_background {
        z-index: 1000;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(0,0,0,0.5);
        width: 100vw;
        height: 100vh;
    }

    .characterCard {
        background: black;
        height: 80vh;
        min-height: 400px;
        min-width: 225px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
    }

    .characterCard .characterCardBody{
        background-image: url('/assets/character_card_background.png');
        position: absolute;
        height: 100%;
        background-size: contain;
    }

    .character_card_background {
        height: 100%;
    }

    .fade-enter-active {
        transition: all .3s ease;
    }
    .fade-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .fade-enter, .slide-fade-leave-to {
        transform: translateX(10px);
        opacity: 0;
    }

    .card-enter-active {
        transition: all .3s ease;
    }
    .card-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .card-enter, .slide-fade-leave-to {
        transform: rotateX(50);
        opacity: 0;
    }
</style>
