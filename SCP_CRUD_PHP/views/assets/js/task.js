const popupFeatures = "width=600,height=500,resizable=yes,scrollbars=yes";

function openCreateTask() {
  const left = (screen.width - 600) / 2;
  const top = (screen.height - 500) / 2;
  window.open(
    "index.php?action=task_create",
    "CreateTask",
    `${popupFeatures},top=${top},left=${left}`,
  );
}

function openEditTask(id) {
  const left = (screen.width - 600) / 2;
  const top = (screen.height - 500) / 2;
  window.open(
    `index.php?action=task_edit&id=${id}`,
    "EditTask",
    `${popupFeatures},top=${top},left=${left}`,
  );
}
