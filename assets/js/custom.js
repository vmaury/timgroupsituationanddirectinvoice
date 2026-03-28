document.addEventListener("DOMContentLoaded", function() {

	// --- Déplacement du bloc ---
	const block = document.querySelector("#timgroup-directinvoices");
	if (!block) return;
	const fichecenter = block.closest(".fichecenter");
	if (fichecenter) {
		const container = document.createElement("div");
		container.style.width = "100%";
		container.appendChild(block);
		const firstHalfLeft = fichecenter.querySelector(".fichehalfleft");
		if (firstHalfLeft) fichecenter.insertBefore(container, firstHalfLeft);
	}
});
