from pdf2image import convert_from_path

pages = convert_from_path("menu.pdf", 500)
for page in pages:
    page.save("menu.jpg", "JPEG")
