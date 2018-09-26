<template id="">
    <transition name="fade">
        <div class="character_card_veil" v-if="open" :key="selectedId" :data-index="selectedId">
            <div id="prevCard" v-on:click="prev" v-if="showPrev">
                <i class="glyphicon glyphicon-chevron-left"></i>
            </div>
            <div id="nextCard" v-on:click="next" v-if="showNext">
                <i class="glyphicon glyphicon-chevron-right"></i>
            </div>
            <transition name="card">
                <div class="characterCard" v-if="characters[index] != null" :key="index" :data-index="index">
                    <span>{{ characters[index].idCharacter + ': ' + characters[index].name}}</span>
                    <div id="closeCard" v-on:click="close">
                        <i class="glyphicon glyphicon-remove"></i>
                    </div>
                    <div class="characterCardBody">
                        <img src="/assets/character_card_background.png" alt="background" class="character_card_background">
                        <img :src="characters[index].image" alt="character" class="characterImage">
                        <img src="/assets/character_card_foreground.png" alt="foreground" class="character_card_foreground">
                        <h2 style="top: 5%;"><b>{{ characters[index].idCharacter }}</b></h2>
                        <h4 style="top: 15%;">{{ characters[index].name }}</h4>
                        <p style="top: 55%;">{{ characters[index].description }}</p>
                        <p style="top: 65%;">
                            <b>Traits:</b> <br>
                            <ul style="padding: 0 40px;">
                                <li v-for="(trait, index) in (characters[index].traits != null ? characters[index].traits.split(/\n/) : [])" id="index">
                                    {{ trait }}
                                </li>
                            </ul>
                        </p>
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
            this.index = this.selectedId;
            this.open = (this.index != null);
        }
    },
    computed: {
        showNext: function() {
            return this.characters.length > 0 && this.index < this.characters.length - 1;
        },
        showPrev: function() {
            return this.characters.length > 0 && this.index > 0;
        }
    },
    methods: {
        open: function() {
            this.open = true;
        },
        close: function() {
            this.open = false;
            this.index = null;
            this.$emit('close');
        },
        next: function() {
            if (this.index < this.characters.length - 1) {
                this.index++;
            }
        },
        prev: function() {
            if (this.index > 0) {
                this.index--;
            }
        }
    }
}
</script>

<style media="screen">
    .character_card_veil {
        z-index: 1000;
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(0,0,0,0.5);
        width: 100vw;
        height: 100vh;
        overflow: auto;
    }

    .characterCard {
        background: linear-gradient(to bottom, #000 0%, rgba(0,0,0,0) 100%);
        height: 80vh;
        min-height: 400px;
        min-width: 225px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
        width: min-content;
        border-top: 2vh solid black;
    }

    .characterCard > span {
        color: white;
        position: absolute;
        top: -2%;
        left: 5px;
    }

    .characterCard .characterCardBody {
        height: 100%;
        color: white;
    }

    .characterCard .characterCardBody .character_card_foreground, .characterCard .characterCardBody .character_card_background {
        height: 100%;
    }

    .characterCard .characterCardBody .character_card_foreground {
        position: absolute;
        transform: translate(-50%,-50%) !important;
        top: 50%;
        left: 50%;
    }

    .characterCard .characterCardBody > *:not(.character_card_background) {
        position: absolute;
        left: 50%;
        transform: translate(-50%);
        width: 100%;
        text-align: center;
    }

    .characterCard .characterImage {
        top: 20%;
        height: 35%;
        width: auto !important;
    }

    #closeCard {
        position: absolute;
        top: -2%;
        right: 2%;
        color: white;
    }

    #nextCard, #prevCard {
        font-size: 3em;
        position: absolute;
        top: 50vh;
        color: white;
        z-index: 1;
    }

    #nextCard {
        right: 5vw;
    }

    #prevCard {
        left: 5vw;
    }

    .fade-enter-active, .fade-leave-active {
      transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
      opacity: 0;
    }

    .card-enter-active {
        transition: all .3s ease;
    }
    .card-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .card-enter, .slide-fade-leave-to {
        transform: rotateY(90deg);
        opacity: 0;
    }
</style>
