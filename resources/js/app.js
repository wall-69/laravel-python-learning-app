import "./bootstrap";
import { createApp } from "vue";
import ExampleComponent from "./components/TestComponent.vue";

const app = createApp({});

app.component("test", ExampleComponent);

app.mount("#app");
