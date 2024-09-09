import sys
from PIL import Image
import pytesseract
import matplotlib.pyplot as plt
from matplotlib.widgets import RectangleSelector
from sympy import sympify
import re
import logging

# Set up logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

def crop_and_extract_latex(image_path, crop_box, output_path):
    try:
        # Open the image
        image = Image.open(image_path)
        logging.info("Image loaded successfully.")

        # Crop the image
        cropped_image = image.crop(crop_box)
        logging.info(f"Cropping the image with box: {crop_box}")

        # Convert to RGB if the image has an alpha channel (RGBA)
        if cropped_image.mode == 'RGBA':
            cropped_image = cropped_image.convert('RGB')
            logging.info("Converted image from RGBA to RGB.")

        # Save the cropped image as JPEG
        cropped_image.save(output_path, "JPEG")
        logging.info(f"Image cropped and saved successfully to {output_path}.")

        # Perform OCR on the cropped image
        text = pytesseract.image_to_string(cropped_image, config='--psm 6')
        logging.info("OCR performed successfully.")
        
        # Convert OCR result to LaTeX
        latex_result = convert_text_to_latex(text)
        logging.info(f"LaTeX Result:\n{latex_result}")

        # Return the OCR and LaTeX result
        return {'ocr': text, 'latex': latex_result}

    except Exception as e:
        logging.error(f"Error during cropping or OCR: {e}")
        return None

def convert_text_to_latex(text):
    """
    Simple conversion of text to LaTeX format.
    This function can be extended based on specific needs.
    """
    replacements = {
        'sqrt': '\\sqrt',
        'integral': '\\int',
        'sum': '\\sum',
        'infinity': '\\infty',
        'alpha': '\\alpha',
        'beta': '\\beta'
        # Add more replacements as needed
    }

    latex_text = text
    for key, value in replacements.items():
        latex_text = latex_text.replace(key, value)

    # Example: handle common mathematical notations
    latex_text = re.sub(r'(\d+)\s+([a-zA-Z]+)', r'\1 \2', latex_text)  # Example: 5x to 5 x
    return latex_text

def onselect(eclick, erelease):
    global crop_box
    crop_box = (int(eclick.xdata), int(eclick.ydata), int(erelease.xdata), int(erelease.ydata))
    logging.info(f"Selected cropping box: {crop_box}")
    plt.close()

def main():
    if len(sys.argv) != 2:
        logging.error("Usage: python ocr_script.py <image_path>")
        return

    image_path = sys.argv[1]
    output_path = image_path.replace('.png', '_cropped.jpg')  # Update to desired output format and location
    
    # Load and display the image
    image = Image.open(image_path)
    fig, ax = plt.subplots()
    ax.imshow(image)
    plt.title("Select a region to perform OCR. Close the window to finish.")

    # Create the RectangleSelector for cropping
    toggle_selector = RectangleSelector(
        ax, onselect,
        useblit=True, button=[1], minspanx=5, minspany=5,
        spancoords='pixels', interactive=True
    )

    def on_key_press(event):
        if event.key == 'q':  # Press 'q' to quit
            logging.info("Quit signal received. Closing the plot.")
            plt.close(fig)

    plt.connect('key_press_event', on_key_press)
    plt.show()

    # Crop the image and perform OCR
    result = crop_and_extract_latex(image_path, crop_box, output_path)
    if result:
        ocr_result = result['ocr']
        latex_result = result['latex']
        logging.info(f"OCR Result:\n{ocr_result}")
        logging.info(f"LaTeX Result:\n{latex_result}")

if __name__ == "__main__":
    main()
