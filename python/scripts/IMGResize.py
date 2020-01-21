from PIL import Image
import os

files1 = [f for f in os.listdir() if f[-4:].lower() in ('.png', '.jpg', '.gif')]
for (index, filename) in enumerate(files1):
    try:
        image = Image.open(filename)
        image.convert("RGB")
        image.save(filename[:-4].replace("_", " ") + ".jpg", optimize=True, quality=90)
        print(filename + " done!")
    except Exception as err:
        print(filename + " failed!" + str(err))
        pass
