export default function createAddButton() {
    const addBtn = document.createElement("button");
    addBtn.classList.add("btn", "btn-primary", "btn-sm");
    addBtn.innerHTML = "+";

    return addBtn;
}
