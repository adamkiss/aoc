extension Calculations on List<int> {
	int sum() {
		int total = 0;

		for (var i in this) {
			total += i;
		}

		return total;
	}

	int product() {
		int total = 1;

		for (var i in this) {
			total *= i;
		}

		return total;
	}
}