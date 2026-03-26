import os
import shutil
import subprocess
import sys


def run_with_host_python():
	if os.environ.get("TK_HOST_REEXEC"):
		return False
	if not shutil.which("flatpak-spawn"):
		return False
	result = subprocess.run(
		["flatpak-spawn", "--host", "python3", __file__],
		env={**os.environ, "TK_HOST_REEXEC": "1"},
		check=False,
	)
	raise SystemExit(result.returncode)


try:
	import tkinter as tk
except ModuleNotFoundError:
	run_with_host_python()
	raise SystemExit(
		"tkinter est absent dans ce Python. Lance le script avec le Python de l'hote."
	)

root = tk.Tk()
tk.Label(root, text="test!").pack()
tk.Button(root, text="test", command=root.quit).pack()
root.mainloop()
