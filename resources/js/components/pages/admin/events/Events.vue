<template>
    <div class="content container py-3 flex-grow-1">
        <h2>Налаштування подій</h2>

        <form @submit.prevent="storeEvent">
            <div class="input-group mb-3">

                <input
                    type="text" class="form-control"
                    placeholder="Нова подія"
                    v-model="newEvent.title"
                >
                <div class="input-group-append">
                    <span type="submit" class="input-group-text" @click="storeEvent">Додати подію</span>
                </div>
            </div>
        </form>
        <div
            v-if="!$v.newEvent.title.required && $v.newEvent.title.$dirty "
            class="text-danger"
        >Введіть назву
        </div>

        <hr>

        <div v-for="event in events">
            <div v-if="editId == -1" class="input-group mb-3">
                <input
                    type="text" class="form-control"
                    placeholder="Нова подія"
                    v-model="event.title"
                    @click="editEvent(event)"
                    readonly
                >
                <div class="input-group-append">
                    <span class="input-group-text" @click="deleteEvent(event.id)">Видалити подію</span>
                </div>
            </div>
            <router-link v-if="editId == -1" tag="span" :to="`/admin/events/${event.id}/questions`">
                <button class="btn btn-info mb-3 mr-3">Додати запитання</button>
            </router-link>
            <router-link v-if="editId == -1" tag="span" :to="`/admin/events/${event.id}/customers`">
                <button class="btn btn-info mb-3 mr-3">Додати користувача</button>
            </router-link>
            <div v-if="editId == event.id" class="input-group mb-3">
                <input
                    type="text" class="form-control"
                    placeholder="Нова подія"
                    v-model="editedEvent.title"
                >
                <div class="input-group-append">
                    <span class="input-group-text" @click="updateEvent">Редагувати</span>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text" @click="cancelEdit">Скасувати</span>
                </div>
            </div>
            <div
                v-if="editId == event.id && !$v.editedEvent.title.required && $v.editedEvent.title.$dirty"
                class="text-danger"
            >Введіть назву
            </div>
        </div>

    </div>
</template>

<script>
import {mapMutations} from "vuex";
import {clone as _clone} from 'lodash';
import {
    required,
    maxLength
} from "vuelidate/lib/validators";

export default {
    data() {
        return {
            editId: -1,
            events: [],
            newEvent: {
                title: ""
            },
            editedEvent: {
                title: ""
            }
        };
    },
    validations: {
        newEvent: {
            title: {
                required,
                maxLength: maxLength(5000),
            },
        },
        editedEvent: {
            title: {
                required,
                maxLength: maxLength(5000),
            },
        }
    },
    mounted() {
        this.getEvents();
    },
    methods:{
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        getEvents() {
            this.setIsShowSpinner(true);
            axios
                .get(`/events`)
                .then(response => {
                    console.log(response.data);
                    this.events = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeEvent() {
            if (this.$v.newEvent.title.$invalid) {
                this.$v.newEvent.title.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`events`, {
                    title: this.newEvent.title,
                })
                .then(response => {
                    this.getEvents();
                })
                .catch(error => {

                })
                .then(() => {
                    this.newEvent.title = "";
                    this.setIsShowSpinner(false);
                });
        },
        editEvent(event) {
            this.editId = event.id;
            this.editedEvent =_clone(event, true);
        },
        cancelEdit () {
            this.editId = -1;
            this.editedEvent = null;
        },
        updateEvent() {
            if (this.$v.editedEvent.title.$invalid) {
                this.$v.editedEvent.title.$touch();
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`events/${this.editId}`, {
                    title: this.editedEvent.title,
                })
                .then(response => {
                    this.getEvents();
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                    this.cancelEdit();
                });
        },
        deleteEvent(eventId) {
            if (window.confirm("Видалити подію?")) {
                this.setIsShowSpinner(true);
                axios
                    .delete(`/events/${eventId}`)
                    .then(response => {
                        this.getEvents();
                    })
                    .catch(error => {
                    })
                    .then(() => {
                        this.setIsShowSpinner(false);
                    });
            }
        },
    }
}
</script>

<style scoped>

.content {
    background-color: rgb(241, 241, 241);
}

</style>
