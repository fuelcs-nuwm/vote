<template>
    <div class="chat d-flex flex-column flex-grow-1 border border-secondary">
        <div class="bg-primary p-2 text-center" role="button">
            Оновити чат
        </div>
        <div class="d-flex flex-grow-1 p-3">
            <div class="flex-grow-1 border-bottom  border-top border-secondary">
                <vuescroll :ops="ops">
                    <div v-for="message in messages">
                        <div class="d-flex flex-column border border-secondary my-2">
                            <div class="bg-white p-2">
                                <span><b>{{ message.user.name }}</b></span> <span>{{ message.date }}</span>
                            </div>
                            <div class="flex-grow-1 p-2">{{ message.message }}</div>
                            <div class="d-flex justify-content-end">
                                <span
                                    class="p-2 border-top border-left"
                                    role="button"
                                    @click="replyMessage(message.id)"
                                >Відповісти</span>
                            </div>
                        </div>

                        <div
                            v-for="replies_message in message.replies"
                            class="d-flex flex-column border border-secondary my-2 ml-3d"
                        >
                            <div class="bg-white p-2">
                                <span><b>{{ replies_message.user.name }}</b></span> <span>{{ replies_message.date }}</span>
                            </div>
                            <div class="flex-grow-1 p-2">{{ replies_message.message }}</div>
                            <div class="d-flex justify-content-end">
                                <span
                                    class="p-2 border-top border-left"
                                    role="button"
                                    @click="replyMessage(replies_message.id)"
                                >Відповісти</span>
                            </div>
                        </div>
                    </div>

                </vuescroll>
            </div>
        </div>
        <div class="input-group p-3">
                <textarea
                    type="text"
                    class="form-control"
                    placeholder="Нове запитання"
                    v-model="newMessage.message"
                    rows="3"
                ></textarea>
            <div class="input-group-append">
                <span v-if="!isReply"class="input-group-text" role="button" @click="storeMessage">Надіслати</span>
                <span v-if="isReply"  class="input-group-text" role="button" @click="storeMessage">Відповісти</span>
            </div>
        </div>
    </div>
</template>

<script>
import vuescroll from "vuescroll";
import {mapMutations} from "vuex";
import {maxLength, required} from "vuelidate/lib/validators";
export default {
    components: {
        vuescroll,
    },
    data() {
        return {
            messages: [],
            newMessage: {
                message: ""
            },

            isReply: false,
            replyMessageId: null,

            ops: {
                vuescroll: {},
                scrollPanel: {},
                rail: {},
                bar: {
                    onlyShowBarOnScroll: true,
                    background: "#ED6E00",
                    //keepShow: true,
                }
            }
        };
    },
    validations: {
        newMessage: {
            message: {
                required,
                maxLength: maxLength(5000),
            },
        },
    },
    mounted() {
        this.init();
    },
    methods: {
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init() {
            this.getMessages();
        },
        getMessages() {
            this.setIsShowSpinner(true);
            axios
                .get(`/messages/event-messages`)
                .then(response => {
                    this.messages = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeMessage() {
            // if (this.$v.newQuestion.title.$invalid) {
            //     this.$v.newQuestion.title.$touch();
            //     return;
            // }
            this.setIsShowSpinner(true);
            axios
                .post(`messages`, {
                    message: this.newMessage.message,
                    message_id: this.replyMessageId,
                })
                .then(response => {
                    this.messages.push(response.data.data);
                })
                .catch(error => {

                })
                .then(() => {
                    this.newMessage.message = "";
                    this.isReply = false;
                    this.replyMessageId = null;
                    this.setIsShowSpinner(false);
                });
        },
        replyMessage (message_id) {
            this.isReply = true;
            this.replyMessageId = message_id;
        }
    }
}
</script>

<style scoped>

</style>
